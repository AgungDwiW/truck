/*
MIT License

Copyright (c) 2020 Dyah PP Wadhana | damar.teduh@gmail.com

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

// Generate list warna
var i;
var newDiv = '';
var json = { 0:"#000000", 1:"#191919", 2:"#323232", 3:"#4b4b4b", 4:"#646464", 5:"#7d7d7d", 6:"#969696", 7:"#c8c8c8", 8:"#e1e1e1", 9:"#ffffff", 10:"#820000", 11:"#9b0000", 12:"#b40000", 13:"#cd0000", 14:"#e60000", 15:"#ff0000", 16:"#ff1919", 17:"#ff3232", 18:"#ff4b4b", 19:"#ff6464", 20:"#ff7d7d", 21:"#823400", 22:"#9b3e00", 23:"#b44800", 24:"#cd5200", 25:"#e65c00", 26:"#ff6600", 27:"#ff7519", 28:"#ff944b", 29:"#ffa364", 30:"#ffb27d", 31:"#ff8532", 32:"#828200", 33:"#9b9b00", 34:"#b4b400", 35:"#cdcd00", 36:"#e6e600", 37:"#ffff00", 38:"#ffff19", 39:"#ffff32", 40:"#ffff4b", 41:"#ffff64", 42:"#ffff7d", 43:"#003300", 44:"#004d00", 45:"#008000", 46:"#00b300", 47:"#00cc00", 48:"#00e600", 49:"#1aff1a", 50:"#4dff4d", 51:"#66ff66", 52:"#80ff80", 53:"#b3ffb3", 54:"#001a4d", 55:"#002b80", 56:"#003cb3", 57:"#004de6", 58:"#0000ff", 59:"#0055ff", 60:"#3377ff", 61:"#4d88ff", 62:"#6699ff", 63:"#80b3ff", 64:"#b3d1ff", 65:"#003333", 66:"#004d4d", 67:"#006666", 68:"#009999", 69:"#00cccc", 70:"#00ffff", 71:"#1affff", 72:"#33ffff", 73:"#4dffff", 74:"#80ffff", 75:"#b3ffff", 76:"#4d004d", 77:"#602060", 78:"#660066", 79:"#993399", 80:"#ac39ac", 81:"#bf40bf", 82:"#c653c6", 83:"#cc66cc", 84:"#d279d2", 85:"#d98cd9", 86:"#df9fdf", 87:"#660029", 88:"#800033", 89:"#b30047", 90:"#cc0052", 91:"#e6005c", 92:"#ff0066", 93:"#ff1a75", 94:"#ff3385", 95:"#ff4d94", 96:"#ff66a3", 97:"#ff99c2" };
for(var k in json){
  newDiv+='<div class="color-option" style="background-color:' + json[k] + '" onclick="cmdPick(\'' + json[k] + '\')"></div>';
}

var colorInput = document.getElementById('colorPicker');
var colorPalette = document.getElementById('colorPalette');

colorInput.addEventListener("click", showColorPalette);
colorInput.addEventListener("focusout", hideColorPalette);
colorPalette.mouseIsOver = false;

colorPalette.onmouseover = function(){
  colorPalette.mouseIsOver = true;
};
colorPalette.onmouseout = function(){
  colorPalette.mouseIsOver = false;
}

function cmdPick(id) {
  colorPalette.mouseIsOver="true";
  colorInput.value = id;
  colorInput.style.borderRight =  '50px solid '+id;
  colorPalette.style.display = 'none';
}

function hideColorPalette() {
  if(colorPalette.mouseIsOver === false) {
    colorPalette.style.display = 'none';
    colorInput.style.borderRight =  '50px solid '+colorInput.value;
  }
}

function showColorPalette() {
  colorPalette.style.display = 'block';
  colorPalette.innerHTML = newDiv;
}