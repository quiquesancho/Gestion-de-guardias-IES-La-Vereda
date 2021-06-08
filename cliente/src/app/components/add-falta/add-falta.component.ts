import { Component, OnInit } from '@angular/core';
import { Falta } from 'src/app/interfaces/falta';
import { UsersService } from 'src/app/services/users.service';

@Component({
  selector: 'app-add-falta',
  templateUrl: './add-falta.component.html',
  styleUrls: ['./add-falta.component.css']
})
export class AddFaltaComponent implements OnInit {

  faltas: Falta[] = [];

  constructor(private userService: UsersService) { }

  ngOnInit(): void {
    const mail = localStorage.getItem('mail');
    this.userService.getFaltas(mail).subscribe(faltas => this.faltas = faltas);
  }

}
