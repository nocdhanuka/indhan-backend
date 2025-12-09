package com.dhanuka.indhan.DemoRequestActivity.models

data class AllVillageListResponse(
    val `data`: ArrayList<Data>,
    val message: String,
    val status: String,
    val status_code: Int
) {
    data class Data(
        val avgVisit: Int,
        val cropList: ArrayList<Crop>,
        val district: String,
        val farmers: Int,
        val state: String,
        val tehsil: String,
        val villageId: String,
        val villageName: String
    ) {
        data class Crop(
            val acres: String,
            val crop: String,
            val farmersCount: String
        )
    }
}