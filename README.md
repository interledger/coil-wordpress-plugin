# Coil for WordPress

**Contributors:** coil, pragmatic  
**Tags:** coil, payment, monetization, traffic  
**Requires at least:** 4.9  
**Requires PHP:** 5.6  
**Tested up to:** 5.2.4  
**Stable tag:** 1.0.0  
**License:** GPL v2 or later  

Enables support for Coil in WordPress.

## 🤑 Features

* Adds the monetize meta tag in your header.
* Supports monetization for Classic Editor and Gutenberg.
* Gutenberg users can monetize at block level.

---

### 💻 Developers

Below you will find some information on how to run scripts for Gutenberg.

>You can find the most recent version of this guide [here](https://github.com/ahmadawais/create-guten-block).

## 👉  `npm start`
- Use to compile and run the block in development mode.
- Watches for any changes and reports back any errors in your code.

## 👉  `npm run build`
> ⚠️ I have found using this command causes the Gutenberg editor to fail because of minification issue so use at your own peril. ⚠️

- Use to build production code for your block inside `dist` folder.
- Runs once and reports back the gzip file sizes of the produced code.

## 👉  `npm run eject`
- Use to eject your plugin out of `create-guten-block`.
- Provides all the configurations so you can customize the project as you want.
- It's a one-way street, `eject` and you have to maintain everything yourself.
- You don't normally have to `eject` a project because by ejecting you lose the connection with `create-guten-block` and from there onwards you have to update and maintain all the dependencies on your own.
