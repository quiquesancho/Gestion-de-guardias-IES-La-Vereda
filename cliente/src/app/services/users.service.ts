import { EventEmitter, Injectable, Output } from '@angular/core';
import { map } from 'rxjs/operators';
import { HttpClient } from '@angular/common/http';
import { User } from '../interfaces/user';



@Injectable({
  providedIn: 'root'
})
export class UsersService {

  URL = 'http://guardias.local';
  @Output() getLoggedInName: EventEmitter<any> = new EventEmitter();
  @Output() role: EventEmitter<any> = new EventEmitter();
  users: User[];
  redirectUrl: string;

  constructor(private httpClient: HttpClient) { }

  userLogin(user: string, pass: string) {
    return this.httpClient.post<User>(this.URL + '/login', { user, pass }).pipe(
      map(user => {
        if (user['message'] == 0) {
          return user;
        } else {
          this.setToken(user['nombre'] + ' ' + user['apellido1'] + ' ' + user['apellido2']);
          this.setRole(user['role']);
          this.setMail(user['email']);
          this.setGuardia(user['g']);
          this.setCheckGuardia(user['c']);
          this.getLoggedInName.emit(true);
          this.role.emit(user['role']);
          return user;
        }
      })
    )
  }

  updateXML(archivo: string) {
    return this.httpClient.post(this.URL + '/subirXML', { archivo }).pipe(
      map(response => {
        console.log(response);
        return response;
      })
    )
  }

  getUsersInGuard() {
    return this.httpClient.get<User[]>(this.URL + '/users').pipe(
      map(response => this.users = response)
    )
  }

  getUsers() {
    return this.httpClient.post<User[]>(this.URL + '/users', {}).pipe(
      map(response => this.users = response)
    )
  }

  comprobarDocenteGurdia(){
    return this.httpClient.get(this.URL + '/docentes').pipe(
      map(response =>{
        console.log(response);
      })
    )
  }

  //token
  setToken(token: string) {
    localStorage.setItem('token', token);
  }

  getToken() {
    return localStorage.getItem('token');
  }

  deleteToken() {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    localStorage.removeItem('mail');
  }

  setRole(role: string) {
    localStorage.setItem('role', role);
  }

  setMail(mail: string) {
    localStorage.setItem('mail', mail);
  }

  setGuardia(guardia: string) {
    localStorage.setItem('guardia', guardia);
  }

  setCheckGuardia(checkGuardia: string) {
    localStorage.setItem('check', checkGuardia);
  }

  isLoggedIn() {
    const usertoken = this.getToken();
    if (usertoken != null) {
      return true
    }
    return false;
  }
}
