package com.dhanuka.indhan.validatefarmers

interface IfcValidationListener {
    fun onIfcCall(position: Int)
    fun onChangeIfcStatus(position: Int)
}