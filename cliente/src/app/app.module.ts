import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from'@angular/forms';
import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LoginComponent } from './components/login/login.component';
import { MenuTopComponent } from './components/menu-top/menu-top.component';
import { WellcomeComponent } from './components/wellcome/wellcome.component';
import { AdminComponent } from './components/admin/admin.component';
import { CalendarioComponent } from './components/calendario/calendario.component';
import { ShowFaltaComponent } from './components/show-falta/show-falta.component';
import { MinDateDirective } from './components/show-falta/validators/min-date.directive';
import { GuardiasComponent } from './components/guardias/guardias.component';


@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    MenuTopComponent,
    WellcomeComponent,
    AdminComponent,
    CalendarioComponent,
    ShowFaltaComponent,
    MinDateDirective,
    GuardiasComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    HttpClientModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
