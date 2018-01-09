import { Injectable } from '@angular/core';
import { Http } from '@angular/http';

import { environment as env } from '../../../../environments/environment';
import { BaseController } from '../../../core/controller/base-controller';

import { SearchEntity } from '../entities/search-entity';

@Injectable()
export class SearchController extends BaseController<SearchEntity> {
  constructor(http: Http) {
    super(http, `${env.apiUrl}/search.php`);
  }

  get(args: { categoryIds: string[] }) {
    return super.get(args);
  }

  post(args: { categoryIds: string[] }) {
    return super.post(args);
  }
}
