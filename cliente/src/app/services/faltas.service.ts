import { Injectable } from '@angular/core';
import { map } from 'rxjs/operators';
import { HttpClient } from '@angular/common/http';
import { Falta } from '../interfaces/falta';

@Injectable({
  providedIn: 'root'
})
export class FaltasService {

  URL = 'http://guardias.local';
  faltas: Falta[];
  falta: Falta;

  constructor(private httpClient: HttpClient) { }

  getFaltas(mail: string) {
    const method = 'get';
    return this.httpClient.post<Falta[]>(this.URL + '/faltas', {method, mail}).pipe(
      map(response => this.faltas = response)
    );
  }

  addFalta(falta: Falta) {
    const method = 'add';
    return this.httpClient.post(this.URL + '/faltas', { method, falta }).pipe(
      map(response => {
        return response;
      })
    )
  }

  deleteFalta(falta: Falta) {
    const method = 'delete';
    return this.httpClient.post<Falta[]>(this.URL + '/faltas', { method, falta }).pipe(
      map(response => {
        this.faltas = response
      })
    )
  }

  updateFalta(falta: Falta, newFecha: string, newHora: string) {
    const method = 'update';
    return this.httpClient.post<Falta>(this.URL + '/faltas', { method, falta, newFecha: newFecha, newHora: newHora }).pipe(
      map( response => this.falta = response)
    )
  }
}
