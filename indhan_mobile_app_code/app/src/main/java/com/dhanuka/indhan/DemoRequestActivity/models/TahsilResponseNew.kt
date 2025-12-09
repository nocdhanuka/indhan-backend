package com.dhanuka.indhan.DemoRequestActivity.models

data class TahsilResponseNew(
    val `data`: List<TehsilDataNew>,
    val message: String,
    val status: String,
    val status_code: Int
)