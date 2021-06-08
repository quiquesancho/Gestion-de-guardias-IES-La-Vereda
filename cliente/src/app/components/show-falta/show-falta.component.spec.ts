import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ShowFaltaComponent } from './show-falta.component';

describe('ShowFaltaComponent', () => {
  let component: ShowFaltaComponent;
  let fixture: ComponentFixture<ShowFaltaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ShowFaltaComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(ShowFaltaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
