# Установка tailwind

Tailwind CSS работает путем сканирования всех ваших файлов HTML, компонентов JavaScript и любых других шаблонов на предмет имен классов, создания соответствующих стилей и последующей записи их в статический файл CSS.

## Установка Tailwind v3
### 1) Установка (CLI)

В корне репозитория:

```bash
npm init -y
npm i -D tailwindcss
```

### 2) После установки появится файл `tailwind.config.js`
Пример настроенной конфигурации:

```js
module.exports = {
  // Включает "dark mode" по классу:
  // чтобы активировать тёмную тему, добавь класс `dark` на <html> или <body>.
  // Пример: <html class="dark">
  darkMode: "class",

  // Пути к файлам, где Tailwind будет искать используемые utility-классы.
  // Важно: указывай все каталоги, где реально пишешь разметку (PHP-шаблоны, компоненты и т.д.),
  // иначе классы не попадут в итоговый CSS.
  content: ["../reg/**/*.php"],

  theme: {
    // Кастомные брейкпоинты (min-width) для адаптивной верстки.
    // Использование: sm:..., md:..., lg:..., xl:..., xxl:...
    // Пример: <div class="md:grid lg:flex">...</div>
    screens: {
      // sm — стили применяются начиная с ширины 576px
      sm: "576px",

      // md — стили применяются начиная с ширины 768px
      md: "768px",

      // lg — стили применяются начиная с ширины 992px
      lg: "992px",

      // xl — стили применяются начиная с ширины 1200px
      xl: "1200px",

      // xxl — стили применяются начиная с ширины 1400px
      xxl: "1400px",
    },

    extend: {
      // Добавляет пользовательские шрифты в theme.fontFamily.
      // После этого можно использовать классы вида:
      // `font-dmSans`, `font-clashDisplay`, `font-body` и т.д.
      // Подключение самих шрифтов (через @font-face/Google Fonts) делается отдельно.
      fontFamily: {
        dmSans: ["DM Sans", "sans-serif"],
        clashDisplay: ["Clash Display", "sans-serif"],
        raleway: ["Raleway", "sans-serif"],
        spaceGrotesk: ["Space Grotesk", "sans-serif"],
        body: ["Inter", "sans-serif"],
      },

      // Добавляет кастомные цвета в theme.colors.
      // Можно использовать как классы:
      // `text-colorOrangyRed`, `bg-colorCodGray`, `border-colorViolet` и т.д.
      colors: {
        colorCodGray: "#191919",
        colorOrangyRed: "#FE330A",
        colorLinenRuffle: "#EFEAE3",
        colorViolet: "#321CA4",
        colorGreen: "#39FF14",
      },
    },
  },

  // Список Tailwind-плагинов.
  // Здесь подключают, например, @tailwindcss/forms, typography, aspect-ratio и т.п.
  plugins: [],
};
```

### 3) Создать главный css файл input.css и добавить в него tw-директивы
`input.css`
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

**@tailwind base**
Подключает базовый слой Tailwind. В него входит CSS Reset (Preflight) и нормализация стандартных браузерных стилей для элементов `html`, `body`, `h1-h6`, `p`, `ul`, `button` и т.д. Это нужно, чтобы элементы отображались одинаково в разных браузерах.

**@tailwind components**
Слой компонентов. Предназначен для пользовательских UI-компонентов и переиспользуемых блоков интерфейса. Здесь обычно объявляют собственные классы, собранные из utility-классов через `@apply`, например кнопки, карточки, формы.

**@tailwind utilities**
Основной слой Tailwind, содержащий utility-классы. Именно здесь генерируются классы для отступов, размеров, цветов, типографики, flex/grid, позиционирования, адаптивности (`sm:`, `md:`, `lg:`) и других утилитарных стилей.

### 4) Запустить процесс tailwind
```bash
npx tailwindcss -i ./src/input.css -o ./src/output.css --watch
```
Не забываем поключить сгенерированные стили в html
```html
<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="./output.css" rel="stylesheet">
</head>
<body>
  <h1 class="text-3xl font-bold underline">
    Hello world!
  </h1>
</body>
</html>
```

### 5) Чтобы не вводить эту команду постоянно настроим `package.json`
`package.json`
```json
{
    "name": "tailwind",
    "version": "1.0.0",
    "description": "",
    "main": "tailwind.config.js",
    "scripts": {
        "start": "npx tailwindcss -i ./src/input.css -o ./src/output.css --watch" // Указываем необходимую команду
    },
    "keywords": [],
    "author": "",
    "license": "ISC",
    "devDependencies": {
        "concurrently": "^8.2.1",
        "live-server": "^1.2.2",
        "prettier": "^3.0.3",
        "prettier-plugin-tailwindcss": "^0.5.5",
        "tailwindcss": "^3.3.3"
    }
}
```
Далее запускаем по команде:
```bash
npx run start
```

## Установка tailwind v4

Tailwind v4 генерирует CSS через CLI: сканирует файлы, где встречаются классы, и пишет результат в статический CSS.

### 1) Установка

В корне репозитория:

```bash
npm init -y
npm install tailwindcss @tailwindcss/cli
```

### 2) Создать входной файл Tailwind

Создай файл:

`src/input.css`

```css
@import "tailwindcss";

/* где искать классы */
@source "../reg/**/*.php";

/* Включаем dark:* по наличию .dark выше по дереву */
@custom-variant dark (&:where(.dark, .dark *));

/* дизайн-токены (v4) */
@theme {
  /* breakpoints -> варианты sm:, md:, ... */
  --breakpoint-sm: 576px;
  --breakpoint-md: 768px;
  --breakpoint-lg: 992px;
  --breakpoint-xl: 1200px;
  --breakpoint-xxl: 1400px;

  /* fonts -> утилиты font-dmSans, font-body, ... */
  --font-dmSans: "DM Sans", sans-serif;
  --font-clashDisplay: "Clash Display", sans-serif;
  --font-raleway: "Raleway", sans-serif;
  --font-spaceGrotesk: "Space Grotesk", sans-serif;
  --font-body: "Inter", sans-serif;

  /* colors -> утилиты bg-colorCodGray, text-colorOrangyRed, ... */
  --color-colorCodGray: #191919;
  --color-colorOrangyRed: #FE330A;
  --color-colorLinenRuffle: #EFEAE3;
  --color-colorViolet: #321CA4;
  --color-colorGreen: #39FF14;
}
```

### 3) Сборка CSS через CLI

**Режим watch (для разработки)**

```bash
npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --watch
```

**Разовая сборка (для коммита/прод)**

```bash
npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --minify
```


### 4) Подключение сгенерированного CSS
```html
<link rel="stylesheet" href="./src/output.css">
```

### 5) Чтобы не вводить команду каждый раз — scripts в `package.json`

В `package.json` добавь:

```json
{
  "scripts": {
    "tw:watch": "npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --watch",
    "tw:build": "npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --minify"
  }
}
```

Запуск:

```bash
npm run tw:watch
```




