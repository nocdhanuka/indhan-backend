package com.dhanuka.indhan.farmerlist


import androidx.room.Entity
import androidx.room.PrimaryKey
import androidx.room.TypeConverters
import com.dhanuka.indhan.roomdatabase.convertors.ProductConverter
import com.google.gson.annotations.SerializedName


data class FarmerBean(
    @SerializedName("data")
    val `data`: MutableList<VillageFarmerData>,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) {
    @Entity
    data class VillageFarmerData(
        @SerializedName("acpVisit")
        val acpVisit: Int,
        @SerializedName("acres")
        val acres: String,
        var village_id: String,
        @SerializedName("alternateContactNumber")
        val alternateContactNumber: String,
        @SerializedName("call_status")
        var callStatus: String,
        @SerializedName("contactNumber")
        val contactNumber: String,
        @SerializedName("district")
        val district: String,
        @SerializedName("farmarImage")
        val farmarImage: String,
        @PrimaryKey @SerializedName("farmerId")
        val farmerId: String,
        @SerializedName("farmerName")
        val farmerName: String,
        @SerializedName("fatherName")
        val fatherName: String,
        @SerializedName("lead_status")
        var leadStatus: String,
        @TypeConverters(ProductConverter::class)
        @SerializedName("products")
        var products: MutableList<FarmerProduct>,
        @SerializedName("state")
        val state: String,
        @SerializedName("tehsil")
        val tehsil: String,
        @SerializedName("farmerLat")
        val farmerLat: String,
        @SerializedName("farmerLong")
        val farmerLong: String
    ) {
        data class FarmerProduct(
            @SerializedName("product")
            val product: String,
            @SerializedName("product_id")
            val productId: String,
            @SerializedName("crop_id")
            val crop_id: String,
            @SerializedName("crop")
            val crop: String,
            @SerializedName("acres")
            val acres: String
        )
    }
}