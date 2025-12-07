import { definePreset } from '@primevue/themes';
import { CarrotOrangeTheme } from './CarrotOrangeTheme';

export const DodgerBlueTheme = definePreset(CarrotOrangeTheme, {
  semantic: {
    primary: {
      50:  '#e6f1fa',
      100: '#b8d9f2',
      200: '#8ac1ea',
      300: '#5ca9e2',
      400: '#3190da',
      500: '#005ed1', // base
      600: '#0052b9',
      700: '#00479e',
      800: '#003c84',
      900: '#003068',
      950: '#00244c',
    },
  },
});
