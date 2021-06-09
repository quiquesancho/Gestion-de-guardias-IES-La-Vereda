import { Component, OnInit } from '@angular/core';
import { UsersService } from 'src/app/services/users.service';

@Component({
  selector: 'app-menu-top',
  templateUrl: './menu-top.component.html',
  styleUrls: ['./menu-top.component.css']
})
export class MenuTopComponent implements OnInit {

  loginbtn: boolean;
  logoutbtn: boolean;
  isDocente: boolean;
  isSecre: boolean;
  isAdmin: boolean;
  estaGuardia: boolean;
  nombreDocente: string;

  constructor(private userService: UsersService) {
    this.userService.getLoggedInName.subscribe(name => this.changeName(name));

    if(this.userService.isLoggedIn()){
      this.loginbtn = false;
      this.logoutbtn = true;
      this.comprobarRol();
      this.nombreDocente = localStorage.getItem('token');
    } else {
      this.loginbtn = true;
      this.logoutbtn = false;
    }
  }

  ngOnInit(): void {
  }

  private changeName(name: boolean): void {
    this.logoutbtn = name;
    this.loginbtn = !name;
    this.comprobarRol();
    this.nombreDocente = localStorage.getItem('token');
  }


  logout() {
    this.userService.deleteToken();
    window.location.href = "/";

  }

  comprobarRol() {
    const role = localStorage.getItem('role');

    if(role == 'docente') {
      this.isDocente = true;
      this.isAdmin = false;
      this.isSecre = false;
      if(localStorage.getItem('guardia') == 'si'){
        this.estaGuardia = true;
      } else {
        this.estaGuardia = false;
      }
    } else if( role == 'admin') {
      this.isDocente = false;
      this.isAdmin = true;
      this.isSecre = false;
    } else if( role == 'secretaria') {
      this.isDocente = false;
      this.isAdmin = false;
      this.isSecre = true;
    }

  }
}
