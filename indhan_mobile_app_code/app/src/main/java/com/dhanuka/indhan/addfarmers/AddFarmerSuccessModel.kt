package com.dhanuka.indhan.addfarmers


//
//data class AddFarmerSuccessModel(
//    @SerializedName("data")
//    val `data`: List<Any>,
//    @SerializedName("message")
//    val message: String,
//    @SerializedName("status")
//    val status: String,
//    @SerializedName("status_code")
//    val statusCode: Int
//)


data class AddFarmerSuccessModel(
        val message: String,
        val status: String,
        val status_code: Int
)