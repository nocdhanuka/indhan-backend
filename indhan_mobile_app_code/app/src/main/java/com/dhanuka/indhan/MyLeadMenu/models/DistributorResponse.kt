package com.dhanuka.indhan.MyLeadMenu.models

data class DistributorResponse(
    val `data`: List<Data>,
    val message: String,
    val status: String,
    val status_code: Int
) {
    data class Data(
        val distributor_id: String,
        val name: String
    )
}