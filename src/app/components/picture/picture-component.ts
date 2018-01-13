import { Component, Input, Output, EventEmitter } from '@angular/core';

import { fullPath } from '../../core/utils';

@Component({
  selector: 'app-picture',
  templateUrl: './picture-component.html',
  styleUrls: ['./picture-component.scss']
})
export class PictureComponent {
  @Input()
  id: string;

  @Input()
  path: string;

  @Input()
  visibleButtons: string[] = ['delete', 'up', 'down', 'download', 'edit'];

  @Output()
  deletePicture = new EventEmitter<string>();

  @Output()
  upPicture = new EventEmitter<string>();

  @Output()
  downPicture = new EventEmitter<string>();

  @Output()
  editPicture = new EventEmitter<string>();

  get rightButtonsHidden() {
    return !this.visibleButtons.some(
      (item) => ['up', 'down', 'download', 'edit'].indexOf(item) >= 0);
  }

  get fullPath(): string {
    console.log(this.visibleButtons);
    return fullPath(this.path);
  }
}
