import React, { useContext, useEffect, useState } from "react";
import axios from "axios";
import { RegistroNotasContext } from "../context/RegistroNotasContext";
import Swal from "sweetalert2";
import { ToastContainer, toast } from "react-toastify";

function Row({ alumno, notasTemplate, asignatura }) {
  const { alumnos, setNotas, successToast, errorToast } =
    useContext(RegistroNotasContext);


  let ponderado = 0;

  let ponderados = notasTemplate.map((nota) => {
    alumno.notas.forEach((notaAlumno) => {
      if (notaAlumno.notaTemplate == nota.id) {
        ponderado =  ( notaAlumno.calificacion * nota.porcentaje / 100).toFixed(2);
        // ponderado += Number( (notaAlumno.calificacion!= null ? Number(notaAlumno.calificacion) : 0) * Number(nota.porcentaje) / 100).toFixed(2);
      }
    });
    return ponderado;
  });

  console.log(ponderados);

  let notaFinal = ponderados.reduce((acc, nota) => {
    return acc + Number(nota);
  }, 0)

  function isNotaValida(notaInput) {
    if ((notaInput < 2 || notaInput > 7) && notaInput != "") {
      Swal.fire({
        icon: "error",
        title: "Nota incorrecta",
        text: "Debe estar entre 2 y 7",
      });
      return false;
    }
    return true;
  }

  function colorScore(event, nota) {
    if (nota < 4) {
      event.target.classList.add("is-invalid");
    } else {
      event.target.classList.add("is-valid");
      event.target.classList.remove("is-invalid");
    }
  }

  function checkNotaFinal() {
    // if(alumno.notas.length == notasTemplate.length ){
    // if (alumno.notas.every((nota) => nota.calificacion != null)) {
    // alumno.notaFinal = alumno.notas.reduce((acc, nota) => {
    let promedio = alumno.notas.reduce((acc, nota) => {
      return acc + Number(nota.calificacion * (nota.porcentaje / 100));
    }, 0);
    $(`.nota-final-${alumno.id}`).val(alumno.notaFinal.toFixed(1));
    // }else{
    //   $(`.nota-final-${alumno.id}`).val('');
    // }
    // }
  }

  const handleInputNota = (e) => {
    const notaInput = e.target.value;
    const alumnoId = e.target.getAttribute("data-alumno");
    const notaId = e.target.getAttribute("data-nota");

    if (notaInput == "") {
      try {
        $.ajax({
          url: "http://localhost/f546/public/index.php/eliminar-nota",
          type: "POST",
          cache: false,
          data: {
            alumnoId: alumnoId,
            notaId: notaId,
          },
          success: function (data) {
            console.log(data);
            if (data.status) {
              successToast("Nota eliminada");
            } else {
              errorToast("Error en eliminar nota");
            }
          },
          error: () => {
            console.error("Error");
          },
          complete: () => {
            console.log("complete");
          },
        });
      } catch (e) {
        console.error(e);
      }
      return false;
    }

    if (!isNotaValida(notaInput)) return false;

    const porcentaje = e.target.getAttribute("data-porcentaje");
    const ponderado = Number((notaInput * porcentaje) / 100).toFixed(2);

    // Asigna ponderado a input siguiente
    $(`.${alumnoId}_${notaId}`).val(ponderado);
    colorScore(e, notaInput);

    try {
      let responseRegistroNota = axios
        .post(
          "registro-nota",
          {},
          {
            params: {
              alumnoId,
              notaId,
              notaInput,
              asignatura,
            },
          }
        )
        .then((response) => {
          if (response.status) {
            alumno.notas = [...response.data.notasAlumno];
            successToast(
              `Nota registrada | ${alumno.nombre} | Nota: ${notaInput}`
            );
            // checkNotaFinal();
          } else {
            errorToast("Error en registro");
          }
        });
      //   console.log(res.data)
    } catch (e) {
      console.error(e);
    }

    if (isNotaValida(notaInput)) {
      // Nota inválida
    } else {
      e.target.value = "";
      $(`.${alumnoId}_${notaId}`).val("");
    }

    checkNotaFinal()
  };



  // function notaFinal(alumno) {
  //   if (alumno.notas.length > 0
  //     // && alumno.notas.every((nota) => nota.calificacion != null)
  //   ) {
  //     alumno.notaFinal = alumno.notas.reduce((acc, nota) => {
  //       return acc + Number((nota.porcentaje / 100) * nota.calificacion);
  //     }, 0);
  //     // $(`.nota-final-${alumno.id}`).val(alumno.notaFinal.toFixed(1))
  //     return alumno.notaFinal;
  //   }
  // }

  function borderScore(nota) {
    if (nota) {
      if (nota >= 4) {
        return "#1cc88a";
      } else {
        return "#e74a3b";
      }
    }
  }

  return (
    <tr>
      <td>{alumno.nombre}</td>

      {/* Se recorren las notas de cursoAsignatura */}
      {notasTemplate.map((nota, index) => {
        if (nota) {
          // if (nota && nota.periodo == "Primer Semestre") {
          // Se busca calificación de alumno en caso de tener registrada
          let calificacion = null;
          let ponderado = null;
          alumno.notas.forEach((notaAlumno) => {
            if (notaAlumno.notaTemplate == nota.id) {
              calificacion = notaAlumno.calificacion;
              ponderado = Number(
                (calificacion * nota.porcentaje) / 100
              ).toFixed(2);
              // return false;
            }
          });

          return (
            <>
              <td key={index}>
                <input
                  data-alumno={alumno.id}
                  data-nota={nota.id}
                  data-porcentaje={nota.porcentaje}
                  type="text"
                  onChange={handleInputNota}
                  defaultValue={calificacion}
                  style={{ borderColor: borderScore(calificacion) }}
                  className="form-control"
                />
              </td>
              <td>
                <input
                  disabled
                  type="text"
                  className={"form-control " + alumno.id + "_" + nota.id}
                  defaultValue={ponderado}
                />
              </td>
            </>
          );
        }
      })}
      <td>
        <input
          disabled
          type="text"
          className={`form-control nota-final-${alumno.id}`}
          defaultValue={notaFinal}
        />
      </td>
    </tr>
  );
}

export default Row;
