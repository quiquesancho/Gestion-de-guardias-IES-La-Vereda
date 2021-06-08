import { Component, OnInit } from '@angular/core';
import { UsersService } from '../../services/users.service';

@Component({
  selector: 'app-admin',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.css']
})
export class AdminComponent implements OnInit {

  arhivoFinal: any;
  codigo: number;
  nombreArchivo: string;

  constructor(private userService: UsersService) { }

  ngOnInit(): void {
  }

  uploadDocument(){
    this.userService.updateXML(this.arhivoFinal).subscribe(data => {
      this.codigo = data['codigo'];
    })
  }

  quit(){
    this.codigo = 99;
  }

  changeImage(fileInput: HTMLInputElement) {
    if (!fileInput.files || fileInput.files.length === 0) { return; }
    const reader: FileReader = new FileReader();
    reader.readAsDataURL(fileInput.files[0]);
    reader.addEventListener('loadend', e => {
      this.arhivoFinal = reader.result as string;
    });
    
  }
}
