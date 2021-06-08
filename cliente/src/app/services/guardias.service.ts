import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs/operators';
import { Guardia } from '../interfaces/guardia';

@Injectable({
  providedIn: 'root'
})
export class GuardiasService {

  URL = 'http://guardias.local';
  guardias: Guardia[];

  constructor(private httpClient: HttpClient) { }

  getGuaridas(){
    return this.httpClient.get<Guardia[]>(this.URL+'/guardias').pipe(
      map(response => this.guardias = response)
    )
  }

  tieneGuardia(){
    const action = 'tieneGuardia'
    const mail = localStorage.getItem('mail');
    return this.httpClient.post(this.URL+'/guardias',{action,email: mail}).pipe(
      map(response => {
        return response;
      })
    )
  }

  confirmGuardia(){
    const action = 'confirm';
    const mail = localStorage.getItem('mail');
    return this.httpClient.post(this.URL+'/guardias', {action ,email: mail}).pipe(
      map((response => {
        return response;
      }))
    )
  }
}
