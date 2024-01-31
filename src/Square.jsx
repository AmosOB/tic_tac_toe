import React, { useState } from 'react'
import 'bootstrap/dist/css/bootstrap.min.css';

const Square = ({ value, onSquareClick }) => {
  return (
    <>
        <button className='btn btn-info' onClick={ onSquareClick }>{ value }</button>
    </>
  )
}

export default Square