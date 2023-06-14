const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            typography: {
                DEFAULT: {
                    css: {
                        h1: {
                            marginBottom: '0.8em',
                            fontSize: '1.9em',
                        },
                        h2: {
                            marginBottom: '0.8em',
                            marginTop: 0,
                        },
                    },
                },
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('tailwind-scrollbar'),
        require('@tailwindcss/typography'),
    ],
};
