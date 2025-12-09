package com.dhanuka.indhan.DemoRequestActivity.model


data class StateResponse(
    val `data`: List<StateData>,
    val message: String,
    val status: String,
    val status_code: Int
)