package com.dhanuka.indhan.DemoRequestActivity.models

data class VillageListNewData(
    val avgVisit: Int,
    val cropList: List<VillageListNewCrop>,
    val district: String,
    val farmers: Int,
    val state: String,
    val tehsil: String,
    val villageId: String,
    val villageName: String
)