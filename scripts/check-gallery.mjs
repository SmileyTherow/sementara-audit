import fs from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';

function gallery(prefix) {
 const image = {src:`${prefix}/main.webp`,alt:'Main'};
 const buttons = ['main','second'].map((name,index)=>({dataset:{gallerySrc:`${prefix}/${name}.webp`,galleryAlt:name},pressed:index===0,active:index===0,setAttribute(key,value){this.pressed=value==='true';},classList:{toggle(name,value){buttons[index].active=value;}},addEventListener(type,handler){this.click=handler;}}));
 return {image,buttons,querySelector(){return image;},querySelectorAll(){return buttons;}};
}
const first=gallery('a'),second=gallery('b');
vm.runInNewContext(fs.readFileSync('resources/js/app.js','utf8'),{document:{getElementById(){return null;},querySelectorAll(selector){return selector==='[data-product-gallery]'?[first,second]:[];}}});
first.buttons[1].click();
assert.equal(first.image.src,'a/second.webp');
assert.equal(first.image.alt,'second');
assert.equal(first.buttons[1].pressed,true);
assert.equal(first.buttons[0].active,false);
assert.equal(second.image.src,'b/main.webp');
first.buttons[0].click();
assert.equal(first.image.src,'a/main.webp');
assert.equal(first.buttons[0].pressed,true);
console.log('PASS: thumbnail selection, accessible state, return to main, independent galleries');
