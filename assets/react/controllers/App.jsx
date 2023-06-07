import React, { useContext } from "react";
import ReactDOM from "react-dom/client";
import { SectionForm } from "./SectionForm";
import {
  RegistroNotasContextProvider,
  RegistroNotasContext,
} from "../context/RegistroNotasContext";

const root = ReactDOM.createRoot(document.getElementById("root"));

function App() {
  useContext(RegistroNotasContext);
  return <SectionForm />
}

root.render(
  <React.StrictMode>
    <RegistroNotasContextProvider>
      <App />
    </RegistroNotasContextProvider>
  </React.StrictMode>
);
