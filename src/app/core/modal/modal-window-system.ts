import { ViewContainerRef, ComponentFactoryResolver } from '@angular/core';

import { ModalDialog } from './modal-dialog';
import {
  ModalAlertComponent
} from '../../components/modal-alert/modal-alert-component';
import {
  ModalConfirmComponent
} from '../../components/modal-confirm/modal-confirm-component';
import {
  ModalErrorComponent
} from '../../components/modal-error/modal-error-component';

type Class<T> = new (...args: any[]) => T;

export class ModalWindowSystem {
  constructor(
    private _resolver: ComponentFactoryResolver,
    private _container: ViewContainerRef) { }

  alert(message: string): Promise<any> {
    return this._createComponent(ModalAlertComponent, message);
  }

  confirm(
    message: string,
    accept?: (data?: any) => void,
    data?: any
  ): Promise<boolean> {
    return this._createComponent(ModalConfirmComponent, message, accept, data);
  }

  error(message: string): Promise<any> {
    return this._createComponent(ModalErrorComponent, message);
  }

  private _createComponent(
    type: Class<ModalDialog>,
    message: string,
    accept?: (data?: any) => void,
    data?: any
  ) {
    return new Promise<boolean>((resolve) => {
      this._container.clear();

      const factory = this._resolver.resolveComponentFactory(type);
      const ref = this._container.createComponent(factory);
      const instance = ref.instance;
      instance.message = message;
      instance.accept.subscribe((response: boolean) => {
        if (response && accept) {
          accept(data);
        }

        ref.destroy();
        resolve(response);
      });
    });
  }
}
