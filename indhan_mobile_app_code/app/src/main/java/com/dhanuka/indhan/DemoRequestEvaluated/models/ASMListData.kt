package com.dhanuka.indhan.DemoRequestEvaluated.models

data class ASMListData(
    val `data`: List<ASMData>,
    val message: String,
    val status: String,
    val status_code: Int
)

data class ASMData(
    val ECN: String,
    val id: Int,
    val name: String,
    val user_type: String
)