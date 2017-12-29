import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { HomeComponent } from './views/home/home-component';
import { UserLoginComponent } from './views/user-login/login-component';
import { UserRegisterComponent } from './views/user-register/user-register-component';
import { PageNotFoundComponent } from './views/page-not-found/page-not-found-component';

const routes: Routes = [
  { path: 'home', component: HomeComponent },
  { path: 'home/:id', component: HomeComponent },
  { path: 'login', component: UserLoginComponent },
  { path: 'user-register', component: UserRegisterComponent },
  { path: '', redirectTo: 'home', pathMatch: 'full' },
  { path: '**', component: PageNotFoundComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
