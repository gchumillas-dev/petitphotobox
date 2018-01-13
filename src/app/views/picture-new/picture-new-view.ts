import { Component, ViewChild, OnInit, ComponentFactoryResolver, ViewContainerRef } from '@angular/core';
import { Location } from '@angular/common';
import { Router, ActivatedRoute } from '@angular/router';

import { ModalWindowSystem } from '../../modules/modal-window-system/modal-window-system';
import { InputCheckboxComponent } from '../../components/input-checkbox/input-checkbox-component';

import { PictureNewController } from './controllers/picture-new-controller';
import { PictureNewEntity } from './entities/picture-new-entity';

@Component({
  selector: 'app-picture-new',
  templateUrl: './picture-new-view.html',
  styleUrls: ['./picture-new-view.scss']
})
export class PictureNewView implements OnInit {
  private _categoryId: string;
  entity: PictureNewEntity;
  modal: ModalWindowSystem;
  paths = new Array<string>();
  showMoreOptions = false;

  constructor(
    private _controller: PictureNewController,
    private _router: Router,
    private _route: ActivatedRoute,
    private _location: Location,
    private _resolver: ComponentFactoryResolver
  ) { }

  @ViewChild('categoriesInput')
  categoriesInput: InputCheckboxComponent;

  @ViewChild('modalContainer', { read: ViewContainerRef })
  modalContainer: ViewContainerRef;

  async ngOnInit() {
    this.modal = new ModalWindowSystem(
      this, this._resolver, this.modalContainer);

    this._route.params.subscribe((params) => {
      this._categoryId = params.categoryId;

      this.modal.loading(async () => {
        try {
          this.entity = await this._controller.get({
            categoryIds: [this._categoryId]
          });
        } catch (e) {
          this.modal.error(e.message);
          throw e;
        }
      });
    });
  }

  onDeletePicture(path: string) {
    const pos = this.paths.indexOf(path);

    if (pos >= 0) {
      this.paths.splice(pos, 1);
    }
  }

  onUpPicture(path: string) {

  }

  onDownPicture(path: string) {

  }

  onPictureUpload(path: string) {
    this.paths.push(path);
  }

  onUploadError(message: string) {
    this.modal.error(message);
  }

  goBack() {
    this._location.back();
  }

  onSubmit() {
    this.modal.loading(async () => {
      const categoryIds = this.categoriesInput.value;

      if (categoryIds.length === 0) {
        this.modal.error('Select at least a category');
        return;
      }

      try {
        this.entity = await this._controller.post({
          categoryIds, title: this.entity.title, tags: this.entity.tags
        });
      } catch (e) {
        this.modal.error(e.message);
        throw e;
      }

      const categoryId = categoryIds.indexOf(this._categoryId) < 0
        ? categoryIds.shift()
        : this._categoryId;
      this._router.navigate([`/home/${categoryId}`]);
    });
  }
}
