import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddFaltaComponent } from './add-falta.component';

describe('AddFaltaComponent', () => {
  let component: AddFaltaComponent;
  let fixture: ComponentFixture<AddFaltaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AddFaltaComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(AddFaltaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
