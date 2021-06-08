import { TestBed } from '@angular/core/testing';

import { GuardiasService } from './guardias.service';

describe('GuardiasService', () => {
  let service: GuardiasService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(GuardiasService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
