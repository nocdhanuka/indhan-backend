package com.dhanuka.indhan.MyVillage


import androidx.room.Entity
import androidx.room.PrimaryKey
import androidx.room.TypeConverters
import com.dhanuka.indhan.roomdatabase.convertors.CropConverter
import com.google.gson.annotations.SerializedName

data class MyVillageBean(
    @SerializedName("data")
    val `data`: MutableList<MyVillageData>,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) {
    @Entity
    data class MyVillageData(
        @SerializedName("avgVisit")
        val avgVisit: Int,
        var isFarmerData: Boolean = false,
        @TypeConverters(CropConverter::class)
        @SerializedName("cropList")
        val cropList: MutableList<MyVillageCrop>,
        @SerializedName("district")
        val district: String,
        @SerializedName("farmers")
        val farmers: Int,
        @SerializedName("state")
        val state: String,
        @SerializedName("tehsil")
        val tehsil: String,
        @PrimaryKey @SerializedName("villageId")
        var villageId: String,
        @SerializedName("villageName")
        var villageName: String
    ) {
        data class MyVillageCrop(
            @SerializedName("crop")
            val crop: String,
            @SerializedName("acres")
            val acres: Any,
            @SerializedName("farmersCount")
            val farmersCount: String
        )
    }
}