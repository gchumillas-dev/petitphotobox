<?php
namespace  petitphotobox\records;
use petitphotobox\core\model\record\DbRecord;
use petitphotobox\core\model\record\DbTable;
use petitphotobox\exceptions\DatabaseError;
use petitphotobox\records\DbCategoryPicture;
use petitphotobox\records\DbPicture;
use petitphotobox\records\DbUser;
use soloproyectos\db\DbConnector;
use soloproyectos\text\Text;

class DbCategory extends DbRecord
{
  private $_user;
  public $parentCategoryId;
  public $title;

  /**
   * Creates a new instance.
   *
   * @param DbConnector $db   Database connection
   * @param DbUser      $user Owner
   * @param string      $id   Record ID (not required)
   */
  public function __construct($db, $user, $id = null)
  {
    $this->_user = $user;
    parent::__construct($db, $id);
  }

  public function getParent()
  {
    return new DbCategory($this->db, $this->_user, $this->parentCategoryId);
  }

  /**
   * Is this category a 'main category'?
   *
   * @return boolean
   */
  public function isMain()
  {
    return Text::isEmpty($this->parentCategoryId);
  }

  /**
   * Gets subcategories from the current category.
   *
   * @return DbCategory[]
   */
  public function getCategories()
  {
    $sql = "
    select
      id
    from category
    where user_id = ?
    and parent_category_id = ?
    order by title";
    $rows = iterator_to_array(
      $this->db->query($sql, [$this->_user->getId(), $this->getId()])
    );

    return array_map(
      function ($row) {
        return new DbCategory($this->db, $this->_user, $row["id"]);
      },
      $rows
    );
  }

  /**
   * Gets the list of 'category pictures' sorted by 'ord'.
   *
   * @return DbCategoryPicture[]
   */
  public function getCategoryPictures()
  {
    $sql = "
    select
      id
    from category_picture
    where category_id = ?
    order by ord desc";
    $rows = iterator_to_array($this->db->query($sql, $this->getId()));

    return array_map(
      function ($row) {
        return new DbCategoryPicture($this->db, $this->_user, $row["id"]);
      },
      $rows
    );
  }

  /**
   * Gets the list of pictures of this category.
   *
   * @return [type] [description]
   */
  public function getPictures()
  {
    $sql = "
    select
      p.id
    from picture as p
    inner join snapshot as s
      on s.picture_id = p.id
    inner join category_picture as cp
      on cp.picture_id = p.id
    inner join category as c
      on c.user_id = ?
      and c.id = cp.category_id
    where cp.category_id = ?
    order by cp.ord desc";
    $rows = iterator_to_array(
      $this->db->query($sql, [$this->_user->getId(), $this->getId()])
    );

    return array_map(
      function ($row) {
        return new DbPicture($this->db, $this->_user, $row["id"]);
      },
      $rows
    );
  }

  /**
   * Gets the category tree.
   *
   * @return array Associative array
   */
  public function getTree()
  {
    return array_map(
      function ($category) {
        return [
          "value" => $category->getId(),
          "label" => $category->title,
          "items" => $category->getTree()
        ];
      },
      $this->getCategories()
    );
  }

  /**
   * Has this category a picture?
   *
   * @param DbPicture $picture A picture
   *
   * @return boolean
   */
  public function hasPicture($picture)
  {
    $pictures = $this->getPictures();

    return count(
      array_filter(
        $pictures,
        function ($row) use ($picture) {
          return $picture->getId() == $row->getId();
        }
      )
    ) > 0;
  }

  /**
   * Adds a new picture to this category.
   *
   * @param DbPicture $picture A picture
   *
   * @return DbCategoryPicture
   */
  public function addPicture($picture)
  {
    if ($this->hasPicture($picture)) {
      throw new DatabaseError("Picture already added");
    }

    $cp = new DbCategoryPicture($this->db, $this->_user);
    $cp->categoryId = $this->getId();
    $cp->pictureId = $picture->getId();
    $cp->save();

    return $cp;
  }

  public function removePicture($picture)
  {
    if (!$this->hasPicture($picture)) {
      throw new DatabaseError("Picture not found");
    }

    $rows = $this->getCategoryPictures();
    foreach ($rows as $row) {
      if ($row->pictureId == $picture->getId()) {
        $row->delete();
      }
    }
  }

  /**
   * {@inheritdoc}
   *
   * @return void
   */
  public function delete()
  {
    $sql = "
    delete from category
    where user_id = ?
    and id = ?";
    $this->db->exec($sql, [$this->_user->getId(), $this->id]);
  }

  /**
   * {@inheritdoc}
   *
   * @return string Record ID
   */
  protected function select()
  {
    $sql = "
    select
      id,
      parent_category_id,
      title
    from category
    where user_id = ?
    and id = ?";
    $row = $this->db->query($sql, [$this->_user->getId(), $this->id]);
    $this->parentCategoryId = $row["parent_category_id"];
    $this->title = $row["title"];

    return $row["id"];
  }

  /**
   * {@inheritdoc}
   *
   * @return void
   */
  protected function update()
  {
    $sql = "
    update category set
      parent_category_id = ?,
      title = ?
    where user_id = ?
    and id = ?";
    $this->db->exec(
        $sql,
        [
          $this->parentCategoryId,
          $this->title,
          $this->_user->getId(),
          $this->id
        ]
    );
  }

  /**
   * {@inheritdoc}
   *
   * @return void
   */
  protected function insert()
  {
    return DbTable::insert(
      $this->db,
      "category",
      [
        "user_id" => $this->_user->getId(),
        "parent_category_id" => $this->parentCategoryId,
        "title" => $this->title
      ]
    );
  }

  /**
   * Searches a category by title.
   *
   * @param DbConnector $db               Database connection
   * @param string      $parentCategoryId Parent category id
   * @param string      $title            Title
   *
   * @return DbCategory
   */
  public static function searchByTitle($db, $parentCategoryId, $title)
  {
    $ret = null;

    $sql = "
    select
      id
    from category
    where parent_category_id = ?
    and title = ?";
    $row = $db->query($sql, [$parentCategoryId, $title]);
    if (count($row) > 0) {
      $ret = new DbCategory($db, $row["id"]);
    }

    return $ret;
  }
}
