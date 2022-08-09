/** @format */

"use strict";

module.exports = {
  env: {
    //amd: true, // Enables require() and define() as global variables.
    browser: true, // Enables browser globals like window and document.
    commonjs: true,
    es6: true,
    //jest: true,
    "jest/globals": true,
    //jquery: true,
    node: true, // Enables Node.js global variables and scoping.
    //qunit: true,
  },
  extends: [
    "eslint:recommended", // https://eslint.org/docs/user-guide/configuring#using-eslintrecommended
    "plugin:@typescript-eslint/eslint-recommended", // `eslint-recommended` is intended to be used with `eslint:recommended`, and it disables all rules that are known to be covered by the typescript typechecker.
    "plugin:@typescript-eslint/recommended", // https://npm.im/@typescript-eslint/eslint-plugin
    "plugin:react/recommended", // https://npm.im/eslint-plugin-react
    "plugin:jsx-a11y/recommended", // https://npm.im/eslint-plugin-jsx-a11y
    "plugin:jsdoc/recommended", // https://npm.im/eslint-plugin-jsdoc
    //"plugin:jest/all", // https://pm.im/eslint-plugin-jest
    "plugin:jest/recommended", // https://pm.im/eslint-plugin-jest
    //"plugin:jest/style", // https://pm.im/eslint-plugin-jest
    //"plugin:prettier/recommended", // https://npm.im/eslint-plugin-prettier
    "prettier", // https://npm.im/eslint-config-prettier#example-configuration
    //"prettier/@typescript-eslint", // https://npm.im/@typescript-eslint/eslint-plugin
    //"prettier/react", // https://npm.im/eslint-plugin-react
  ],
  globals: {
    //Atomics: "readonly"
    //ClipboardJS: true,
    React: "writable",
    //SharedArrayBuffer: "readonly",
    backendData: {},
    context: true, // Cypress.
    cy: true,
    //dataLayer: true,
    //digitalData: true,
    //pikBroker: true,
    //ymaps: true,
  },
  parser: "@typescript-eslint/parser",
  //parser: "babel-eslint",
  parserOptions: {
    ecmaFeatures: { jsx: true },
    ecmaVersion: 11, // Allows for the parsing of modern ECMAScript features.
    //extraFileExtensions: [".vue"],
    //project: ["./tsconfig.json"],
    sourceType: "module", // Allows for the use of imports.
    //tsconfigRootDir: __dirname,
  },
  plugins: [
    //"@typescript-eslint",
    //"jest",
    //"jsdoc",
    //"jsx-a11y",
    //"react",
    //"prettier",
  ],
  root: true, // Make sure eslint picks up the config at the root of the directory.
  rules: {
    //"prettier/prettier": "error",
    //"no-unused-vars": ["error", { varsIgnorePattern: "$event" }],
    //"@typescript-eslint/explicit-module-boundary-types": "off",
    "@typescript-eslint/ban-ts-comment": "off",
    "@typescript-eslint/no-var-requires": "off",
    "jsdoc/check-tag-names": ["error", { definedTags: ["format"] }],
    "react/jsx-sort-props": 2,
    "react/prop-types": "off", // Disable prop-types as we use TypeScript for type checking.
    //"no-prototype-builtins": 1,
  },
  settings: {
    /*"jest": {
      "version": 26
    },*/
    /*linkComponents: [
      // Components used as alternatives to <a> for linking, eg. <Link to={ url } />
      "Hyperlink",
      { name: "Link", linkAttribute: "to" },
    ],
    propWrapperFunctions: [
      // The names of any function used to wrap propTypes, e.g. `forbidExtraProps`. If this isn't set, any propTypes wrapped in a function will be skipped.
      "forbidExtraProps",
      { property: "freeze", object: "Object" },
      { property: "myFavoriteWrapper" },
    ],*/
    react: {
      //createClass: "createReactClass", // Regex for Component Factory to use,
      // default to "createReactClass"
      //flowVersion: "0.53", // Flow version
      //pragma: "React", // Pragma to use, default to "React"
      version: "detect", // React version. "detect" automatically picks the version you have installed.
      // You can also use `16.0`, `16.3`, etc, if you want to override the detected value.
      // default to latest and warns if missing
      // It will default to "detect" in the future
    },
  },
};
