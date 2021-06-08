import { Directive, Input } from '@angular/core';
import { Validator, NG_VALIDATORS, AbstractControl, ValidationErrors } from '@angular/forms'

@Directive({
  selector: '[appMinDate]',
  providers: [{
    provide: NG_VALIDATORS, useExisting: MinDateDirective,
    multi:true
  }]
})
export class MinDateDirective implements Validator {

  @Input() minDate;
  constructor() {}
  validate(c: AbstractControl): { [key: string]: any } {
    if (this.minDate && c.value) {
      const dateControl = new Date(c.value);
      const dateMin = new Date(this.minDate);
      if (dateMin > dateControl) {
        return { minDate: true };
      }
    }
    return null;
  }

}
