/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        gray: {
          50: '#f9fafb',
          100: '#f3f4f6',
          200: '#e5e7eb',
          300: '#bfc4cd',
          400: '#9ca3af',
          500: '#6b7280',
          600: '#4b5563',
          700: '#374151',
          800: '#1f2937',
          900: '#111827',
          950: '#030712',
        },
        violet: {
          50: '#f1eeff',
          100: '#e6e1ff',
          200: '#d2cbff',
          300: '#b7acff',
          400: '#9c8cff',
          500: '#8470ff',
          600: '#755ff8',
          700: '#5d47de',
          800: '#4634b1',
          900: '#2f227c',
          950: '#1c1357',
        },
        sky: {
          50: '#e3f3ff',
          100: '#d1ecff',
          200: '#b6e1ff',
          300: '#a0d7ff',
          400: '#7bc8ff',
          500: '#67bfff',
          600: '#56b1f3',
          700: '#3193da',
          800: '#1c71ae',
          900: '#124d79',
          950: '#0b324f',
        },
      },
      fontFamily: {
        inter: ['Inter', 'sans-serif'],
      },
      fontSize: {
        xs: ['0.75rem', { lineHeight: '1.5' }],
        sm: ['0.875rem', { lineHeight: '1.5715' }],
        base: ['1rem', { lineHeight: '1.5', letterSpacing: '-0.01em' }],
        lg: ['1.125rem', { lineHeight: '1.5', letterSpacing: '-0.01em' }],
        xl: ['1.25rem', { lineHeight: '1.5', letterSpacing: '-0.01em' }],
        '2xl': ['1.5rem', { lineHeight: '1.33', letterSpacing: '-0.01em' }],
        '3xl': ['1.88rem', { lineHeight: '1.33', letterSpacing: '-0.01em' }],
        '4xl': ['2.25rem', { lineHeight: '1.25', letterSpacing: '-0.02em' }],
        '5xl': ['3rem', { lineHeight: '1.25', letterSpacing: '-0.02em' }],
        '6xl': ['3.75rem', { lineHeight: '1.2', letterSpacing: '-0.02em' }],
      },
      boxShadow: {
        xs: '0 0 0 1px rgba(0, 0, 0, 0.05)',
        sm: '0 1px 1px 0 rgb(0 0 0 / 0.05), 0 1px 2px 0 rgb(0 0 0 / 0.02)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    // add sidebar-expanded variant
    function ({ addVariant, e }) {
      addVariant('sidebar-expanded', ({ modifySelectors, separator }) => {
        modifySelectors(({ className }) => {
          return `.sidebar-expanded .${e(`sidebar-expanded${separator}${className}`)}`;
        });
      });
      addVariant('not-checked', '&:not(:checked)');
    },
  ],
}
