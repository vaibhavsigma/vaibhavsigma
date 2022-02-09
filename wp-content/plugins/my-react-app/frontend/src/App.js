import logo from './logo.svg';
import { useState, useEffect } from "react";
import Layout from "./Layout";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import './App.css';
import DemoForm from './DemoForm';
import 'bootstrap/dist/css/bootstrap.min.css';
import NoPage from "./NoPage";
import Offer from "./Offer";

function App() {
  return (
    <div className="App">
      <h1>Start ReactJS!</h1>
      <BrowserRouter>
        <Routes>
          <Route path="/new-starter/" 
                 element={<DemoForm
                        radio={["Gender",["male","female","do not tell"]]}
                        checkbox={['Hobby',["Swimming","Reading", "Writing", "Travelling", "Solving puzzles"]]}
                        select={['Age',["Select Your Age", "Under 18","Between 18 to 35", "Between 36 to 48", "Between 49 to 59", "Above 60"],true]} />} 
                        />
          <Route path="/new-starter/react-app-test/" 
                 element={<DemoForm
                        radio={["Gender",["male","female","do not tell"]]}
                        checkbox={['Hobby',["Swimming","Reading", "Writing", "Travelling", "Solving puzzles"]]}
                        select={['Age',["Select Your Age", "Under 18","Between 18 to 35", "Between 36 to 48", "Between 49 to 59", "Above 60"],true]} />} 
                        />
          <Route path="/new-starter/offers" element={<Offer />} />
          <Route path="*" element={<NoPage />} />
        </Routes>
      </BrowserRouter>
    </div>
  );
}

export default App;
