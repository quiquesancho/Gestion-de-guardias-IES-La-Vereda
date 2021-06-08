import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AdminComponent } from './components/admin/admin.component';
import { CalendarioComponent } from './components/calendario/calendario.component';
import { GuardiasComponent } from './components/guardias/guardias.component';
import { LoginGuard } from './components/login/guards/login.guard';
import { LoginComponent } from './components/login/login.component';
import { ShowFaltaComponent } from './components/show-falta/show-falta.component';
import { WellcomeComponent } from './components/wellcome/wellcome.component';

const routes: Routes = [
  {
    path: '',
    component: WellcomeComponent,
    pathMatch: 'full'
  },
  {
    path: 'login',
    component: LoginComponent
  },
  {
    path: 'calendario',
    component: CalendarioComponent,
    canActivate: [LoginGuard]
  },
  {
    path: 'guardias',
    component: GuardiasComponent,
    canActivate: [LoginGuard]
  },
  {
    path: 'falta/add',
    component: ShowFaltaComponent,
    canActivate: [LoginGuard]
  },
  {
    path: 'subirxml',
    component: AdminComponent,
    canActivate: [LoginGuard]
  },
  {
    path: '**',
    component: WellcomeComponent,
  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
