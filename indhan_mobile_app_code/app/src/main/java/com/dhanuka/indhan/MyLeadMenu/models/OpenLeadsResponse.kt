package com.dhanuka.indhan.MyLeadMenu.models

import java.io.Serializable

data class OpenLeadsResponse(
    val `data`: List<OpenLeadData>,
    val message: String,
    val status: String,
    val status_code: Int
) : Serializable