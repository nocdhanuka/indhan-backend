package com.dhanuka.indhan.VillageDetails


import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class VillageDetailsModel(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String, // Village Detail
    @SerializedName("status")
    val status: String, // success
    @SerializedName("status_code")
    val statusCode: Int // 200
) : Serializable {
    data class Data(
        @SerializedName("avgVisit")
        val avgVisit: Int, // 12
        @SerializedName("cropList")
        val cropList: MutableList<Crop>,
        @SerializedName("district")
        val district: String, // Nainital
        @SerializedName("farmers")
        val farmers: Int, // 2
        @SerializedName("state")
        val state: String, // Uttarakhand
        @SerializedName("tehsil")
        val tehsil: String, // Haldwani
        @SerializedName("villageId")
        val villageId: String, // 3
        @SerializedName("villageName")
        val villageName: String // Lamachor
    ) : Serializable {
        data class Crop(
            @SerializedName("acres")
            val acres: String, // 25
            @SerializedName("crop")
            val crop: String, // Other Fruits
            @SerializedName("farmersCount")
            val farmersCount: String // 2
        ) : Serializable
    }
}