
    <style>
     @font-face { font-family: LiberationSerif; src: url('LiberationSerif-Regular.ttf'); }
      p1 {
         font-family: LiberationSerif; font-size: 24px; color: #cbcbc3;

      }

#main_text {
  display: flex;
  flex-flow: column nowrap;
  justify-content: space-evenly;
  align-items: flex-start;

  margin-left: 7.3%;
  margin-right: 7.3%;
  margin-bottom: 25px;
   background-color: #172136;
  padding: 10px;
}

#whole_text {
  display: flex;
  flex-flow: column nowrap;
  justify-content: space-evenly;
  align-items: flex-start;
}

.chunk {
  white-space: nowrap;
}
#new_text{
  margin-left: 7.3%;
  margin-right: 7.3%;
  background-color: #172136;
  padding: 10px;
}
#tt_button {
 
  background-color: #071022;
  border-style: solid;
  border-radius: 2px;
  padding: 3px;
  z-index: 1;
  color: #cbcbc3;
  font-family: Calibri;
}

#select_button {

  background-color: #071022;
  border-style: solid;
  border-radius: 2px;
  padding: 6px;
  z-index: 1;
  color: #cbcbc3;
  font-family: Calibri;
  font-size: 20px;

}

#lang_button {
  position: absolute;
  background-color: #071022;
 /* border-style: solid; */
  border-radius: 2px;
  padding: 6px; 
  z-index: 1;
  color: #cbcbc3;
  font-family: Calibri;
  font-size: 18px;

}

#textselect, #langselect {
  background-color: /* #172136; */ #071022;
 /* border-style: solid;
  border-radius: 2px; 
  padding: 3px;  */
  z-index: 1;
  color: #cbcbc3;
  font-family: Calibri;
  font-size: 18px;
  min-width: 100px;

} 

#newtext {
  position: flex;
  width: 70%;
  height: 200px;
  border-style: solid;
  border-radius: 5px;
  background-color: #071022;
  color: #cbcbc3;
  text-indent: 0%;
  font-family: Calibri;
  font-size: 18px;
}

#text_title {
  position: flex;
  width: 70%;
  height: 50px;
  border-style: solid;
  border-radius: 5px;
  background-color: #071022;
  color: #cbcbc3;
  text-indent: 0%;
  font-size: 24px;
  font-family: Calibri;
}

.submit_btn {
  border-style: solid;
  padding: 5px;
  border-radius: 2px;
  background-color: #071022;
  color: #cbcbc3;
  font-family: Calibri;
  top: 10px;
  position:relative;
  font-size: 17px;
  left: 10px;
  cursor: pointer;
}

.submit_btn:hover {
  background-color: #040a16;
} 

#title {
  font-size: 26px;
  font-family: LiberationSerif;
  color: rgb(252, 119, 119);
  text-align: center;
  margin-left: 40px;
  margin-right: 40px;
  position: flex;
}

#loadingbutton {
  position: fixed;
  left: 0;
  bottom: 0;

  background-color: #071022;
  border-style: solid;
  border-radius: 2px;
  padding: 6px; 
  z-index: 1;
  color: #cbcbc3;
  font-family: Calibri;
  font-size: 17px;

}


</style>
