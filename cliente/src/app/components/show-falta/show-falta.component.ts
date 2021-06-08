import { Component, OnInit, ViewChild } from '@angular/core';
import { NgForm, NgModel } from '@angular/forms';
import { Falta } from 'src/app/interfaces/falta';
import { User } from 'src/app/interfaces/user';
import { FaltasService } from 'src/app/services/faltas.service';
import { UsersService } from 'src/app/services/users.service';
import { MinDateDirective } from './validators/min-date.directive';

@Component({
  selector: 'app-show-falta',
  templateUrl: './show-falta.component.html',
  styleUrls: ['./show-falta.component.css']
})
export class ShowFaltaComponent implements OnInit {

  newFalta: Falta;
  editedFalta: Falta;
  delete: Falta;
  users: User[];
  faltas: Falta[] = [];
  showProfesor: boolean;
  isEditing: boolean;
  correctFalta: boolean;
  sameFalta: boolean;
  deletedFalta: boolean;
  addCorrectFalta: boolean;
  updateCorrectFalta: boolean;
  @ViewChild('faltaForm', { static: true }) faltaForm: NgForm;

  constructor(private faltasService: FaltasService, private usersService: UsersService) { }

  ngOnInit(): void {
    if (localStorage.getItem('role') == 'admin' || localStorage.getItem('role') == 'secretaria') {
      this.showProfesor = true;
    } else {
      this.showProfesor = false;
    }
    this.isEditing = false;
    this.getFaltas();
    this.usersService.getUsers().subscribe(users => this.users = users);
    this.resetFormFalta();
    this.correctFalta = true;
    this.sameFalta = false;
    this.deletedFalta = false;
    this.addCorrectFalta = false;
  }

  addFalta() {
    if (this.faltaForm.valid) {
      if (!this.showProfesor) {
        this.newFalta.email = localStorage.getItem('mail');
      }

      this.faltasService.addFalta(this.newFalta).subscribe(falta => {
        if (falta['codigo'] == 0) {
          this.correctFalta = false;
          this.resetFormFalta();
        } else if (falta['codigo'] == 2) {
          this.sameFalta = true;
          this.resetFormFalta();
        } else if(falta['codigo'] == 1) {
          this.addCorrectFalta = true;
        }

        this.getFaltas();
        this.resetFormFalta();
      })
    }
  }

  getFaltas() {
    if (localStorage.getItem('role') == 'admin' || localStorage.getItem('role') == 'secretaria') {
      this.faltasService.getFaltas('all').subscribe(faltas => this.faltas = faltas);
    } else {
      this.faltasService.getFaltas(localStorage.getItem('mail')).subscribe(faltas => this.faltas = faltas);
    }
  }

  deleteFalta(falta: Falta) {
    const option = confirm('Seguro que quieres eliminar esta falta?');

    if (option) {
      this.faltasService.deleteFalta(falta).subscribe(faltas => {

        this.deletedFalta = true;
        this.getFaltas();
      })

    }

  }

  editFalta(falta) {
    falta.isEditing = true;
    const date = falta.fecha.split('-');
    this.editedFalta.fecha = date[2] + '-' + date[1] + '-' + date[0];
    this.editedFalta.hora = falta.hora;
  }

  saveFalta(falta) {
    this.faltasService.updateFalta(falta, this.editedFalta.fecha, this.editedFalta.hora).subscribe(data => {
      if (data['error'] == 0) {
        this.correctFalta = true;
        const date = falta.fecha.split('-');
        this.editedFalta.fecha = date[2] + '-' + date[1] + '-' + date[0];
        this.editedFalta.hora = falta.hora;
      } else {
        this.updateCorrectFalta = true;
        this.getFaltas();
      }
    });
    this.isEditing = false;
  }

  quit() {
    this.correctFalta = true;
    this.sameFalta = false;
    this.deletedFalta = false;
    this.addCorrectFalta = false;
    this.updateCorrectFalta = false;
  }

  comprobarFecha() {
    if (!this.isEditing) {
      const dateToday = new Date();
      const dateFalta = new Date(this.newFalta.fecha);

      if (dateToday < dateFalta) {
        console.log(dateFalta);
      } else {
        console.log(dateToday);
      }
    }
  }

  validClasses(ngModel: NgModel, validClass: string, errorClass: string) {
    return {
      [validClass]: ngModel.touched && ngModel.valid,
      [errorClass]: ngModel.touched && ngModel.invalid,
    };
  }

  resetFormFalta() {
    this.newFalta = {
      email: 'Selecciona un profesor...',
      fecha: '',
      hora: 'Todo el día',
      user: {
        email: '',
        dni: '',
        nombre: '',
        apellido1: '',
        apellido2: '',

      }
    }

    this.editedFalta = {
      email: 'Selecciona un profesor...',
      fecha: '',
      hora: '',
      user: {
        email: '',
        dni: '',
        nombre: '',
        apellido1: '',
        apellido2: ''
      }
    }

  }
}

