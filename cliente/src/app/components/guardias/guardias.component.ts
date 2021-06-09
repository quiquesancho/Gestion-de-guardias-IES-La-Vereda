import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Guardia } from 'src/app/interfaces/guardia';
import { User } from 'src/app/interfaces/user';
import { GuardiasService } from 'src/app/services/guardias.service';
import { UsersService } from 'src/app/services/users.service';

@Component({
  selector: 'app-guardias',
  templateUrl: './guardias.component.html',
  styleUrls: ['./guardias.component.css']
})
export class GuardiasComponent implements OnInit {

  isChecked: boolean;
  isAdmin: boolean;
  haveGuardia: number;
  guardias: Guardia[] = [];
  users: User[];
  guardia: Guardia;
  updated: number;
  emailAsignar: string;

  constructor(private guardiasService: GuardiasService, private usersService: UsersService) { }

  ngOnInit(): void {
    if (localStorage.getItem('check') == 'si' || localStorage.getItem('role') == 'admin') {
      if(localStorage.getItem('role') == 'admin'){
        this.isAdmin = true;
      } else {
        this.isAdmin = false;
      }
      this.isChecked = true;
      this.tieneGuardia();
    }
    this.getGuaridias();
    this.obtenerUsuariosGuardia();
  }

  getGuaridias(){
    this.guardiasService.getGuaridas().subscribe(response => {
      this.guardias = response;
    })
  }

  obtenerUsuariosGuardia(){
    this.usersService.obtenerDocenteGurdia().subscribe(response => this.users = response)
  }

  tieneGuardia(){
    this.guardiasService.tieneGuardia().subscribe(response => {
      this.haveGuardia = response['codigo'];
      console.log(response);
      if(this.haveGuardia == 1){
        console.log('Debería de mostrarse esto');
        this.guardia = response['guardia'];
        console.log(response['guardia']);
      }
    } )
  }

  asignarGuardia(guardia){
    let email;
    if(localStorage.getItem('role') == 'admin'){
      email = this.emailAsignar
    } else {
      email = localStorage.getItem('mail');
    }
    this.guardiasService.asignarGuardia(guardia,email).subscribe(data => {
      if(data['codigo'] == 1){
        location.reload();
      } else {
        console.log(data);
      }
    })
  }

  updateObservaciones(guardia){
    this.guardiasService.updateObservaciones(guardia).subscribe(data =>{
      if(data['codigo'] == '1'){
        this.updated = 1;
      }
    });
  }

  confirmarGuardia(){
    this.guardiasService.confirmGuardia().subscribe(response => {
      if(response['codigo'] == 1){
        this.isChecked = true;
        localStorage.setItem('check','si');
        location.reload();
      } else {
        console.log(response)
      }
    });
  }

  quit(){
    this.updated = 0;
  }

}
