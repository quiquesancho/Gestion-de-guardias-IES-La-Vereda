import { Component, OnInit } from '@angular/core';
import { Guardia } from 'src/app/interfaces/guardia';
import { User } from 'src/app/interfaces/user';
import { GuardiasService } from 'src/app/services/guardias.service';

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

  constructor(private guardiasService: GuardiasService) { }

  ngOnInit(): void {
    if (localStorage.getItem('check') == 'si' || localStorage.getItem('role') == 'admin') {
      if(localStorage.getItem('role') == 'admin'){
        this.isAdmin = true;
      } else {
        this.isAdmin = false;
        this.tieneGuardia();
      }
      this.isChecked = true;
    }
    this.getGuaridias();
  }

  getGuaridias(){
    this.guardiasService.getGuaridas().subscribe(response => {
      this.guardias = response;
    })
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
    console.log(guardia);
  }

  updateObservaciones(){
    console.log(this.guardia.observacion);
  }

  confirmarGuardia(){
    this.guardiasService.confirmGuardia().subscribe(response => {
      if(response['codigo'] == 1){
        this.isChecked = true;
        localStorage.setItem('check','si');
      } else {
        console.log(response)
      }
    });
  }

}
