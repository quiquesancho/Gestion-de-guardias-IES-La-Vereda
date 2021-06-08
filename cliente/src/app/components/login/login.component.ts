import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Login } from 'src/app/interfaces/login';
import { User } from 'src/app/interfaces/user';
import { UsersService } from 'src/app/services/users.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  login: Login;
  user: User;
  loginFail: boolean;

  constructor(private userService: UsersService, private router: Router) { }

  ngOnInit(): void {
    this.login = {
      user: '',
      pass: ''
    }
  }

  IniciarSesion(): void {
    this.userService.userLogin(this.login.user, this.login.pass).subscribe(
      data => {
        if(data['message'] == 0){
          this.loginFail = true;
        } else {
          this.user = {
            email: data['email'],
            dni: data['dni'],
            nombre: data['nombre'],
            apellido1: data['apellido1'],
            apellido2: data['apellido2']
          }
  
          this.router.navigate(['/calendario']);
        }

      }
    )
  }

  quit(){
    this.loginFail = false;
  }

}
