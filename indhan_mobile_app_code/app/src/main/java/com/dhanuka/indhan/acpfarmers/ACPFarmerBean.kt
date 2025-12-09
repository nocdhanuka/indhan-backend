package com.dhanuka.indhan.acpfarmers


import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class ACPFarmerBean(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) : Serializable {
    data class Data(
        @SerializedName("ACPDetail")
        val aCPDetail: MutableList<ACPDetail>,
        @SerializedName("DDList")
        val dDList: MutableList<DDList>,
        @SerializedName("totalACPFamer")
        val totalACPFamer: Int,
        @SerializedName("totalAcres")
        val totalAcres: Int,
        @SerializedName("totalClosedAcres")
        val totalClosedAcres: Int,
        @SerializedName("total_leads")
        val totalLeads: Int
    ) : Serializable {
        data class ACPDetail(
            @SerializedName("acpVisit")
            val acpVisit: Int,
            @SerializedName("acres")
            val acres: String,
            @SerializedName("alternateContactNumber")
            val alternateContactNumber: String,
            @SerializedName("call_status")
            val callStatus: String,
            @SerializedName("contactNumber")
            val contactNumber: String,
            @SerializedName("crops")
            val crops: List<Crop>,
            @SerializedName("district")
            val district: String,
            @SerializedName("districtId")
            val districtId: String,
            @SerializedName("farmarImage")
            val farmarImage: String,
            @SerializedName("farmerId")
            val farmerId: String,
            @SerializedName("farmerLat")
            val farmerLat: String,
            @SerializedName("farmerLong")
            val farmerLong: String,
            @SerializedName("farmerName")
            val farmerName: String,
            @SerializedName("fatherName")
            val fatherName: String,
            @SerializedName("lead_status")
            val leadStatus: String,
            @SerializedName("name")
            val name: String,
            @SerializedName("products")
            val products: List<Product>,
            @SerializedName("state")
            val state: String,
            @SerializedName("stateId")
            val stateId: String,
            @SerializedName("tehsil")
            val tehsil: String,
            @SerializedName("tehsilId")
            val tehsilId: String,
            @SerializedName("village")
            val village: String,
            @SerializedName("village_id")
            val villageId: String,
            @SerializedName("leads")
            val leads: List<Leads>
        ) : Serializable {
            data class Crop(
                @SerializedName("crop")
                val crop: String,
                @SerializedName("crop_id")
                val cropId: String
            ) : Serializable

            data class Product(
                @SerializedName("acres")
                val acres: String,
                @SerializedName("crop")
                val crop: String,
                @SerializedName("crop_id")
                val cropId: String,
                @SerializedName("product")
                val product: String,
                @SerializedName("product_id")
                val productId: String
            ) : Serializable
            data class Leads(
                @SerializedName("lead_id")
                val leadId: String,
                @SerializedName("lead_code")
                val leadCode: String,
                @SerializedName("lead_status")
                val leadStatus: String,
                @SerializedName("created_at")
                val createdAt: String,
                @SerializedName("expected_purchase_date")
                val expectedPurchaseDate: String,
                @SerializedName("purchased_from")
                val purchasedFrom: String,
                @SerializedName("purchased_area")
                val purchasedArea: String
            ) : Serializable

        }

        data class DDList(
            @SerializedName("dd_id")
            val ddId: String,
            @SerializedName("ECN")
            val eCn: String,
            @SerializedName("name")
            val name: String
        ) : Serializable
    }
}