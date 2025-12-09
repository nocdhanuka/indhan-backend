package com.dhanuka.indhan.Reports.Model


import com.google.gson.annotations.SerializedName

data class ReportsResponse(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) {
    data class Data(
        @SerializedName("Active_Count")
        val activeCount: String,
        @SerializedName("Active_Sale_Count")
        val activeSaleCount: String,
        @SerializedName("Area_Count")
        val areaCount: String,
        @SerializedName("Average_Farmers")
        val averageFarmers: String,
        @SerializedName("Average_Validate_Farmers")
        val averageValidateFarmers: String,
        @SerializedName("BDO_Count")
        var bDOCount: Int,
        @SerializedName("Closed_Count")
        val closedCount: String,
        @SerializedName("Closed_Sale_Count")
        val closedSaleCount: String,
        @SerializedName("demo_awaited")
        val demoAwaited: Int,
        @SerializedName("demo_evaluated")
        val demoEvaluated: Int,
        @SerializedName("demo_overdue")
        val demoOverdue: Int,
        @SerializedName("Follow_Sale_Up_Count")
        val followSaleUpCount: String,
        @SerializedName("Follow_Up_Count")
        val followUpCount: String,
        @SerializedName("Regional_Count")
        val regionalCount: String,
        @SerializedName("Sales_Area_Count")
        val salesAreaCount: String,
        @SerializedName("Sales_Regional_Count")
        val salesRegionalCount: String,
        @SerializedName("Testimonial")
        val testimonial: String,
        @SerializedName("totalACPFamer")
        val totalACPFamer: Int,
        @SerializedName("totalAcres")
        val totalAcres: Int,
        @SerializedName("Total_Activities")
        var totalActivities: String,
        @SerializedName("totalClosedAcres")
        val totalClosedAcres: Int,
        @SerializedName("Total_Farmers")
        val totalFarmers: String,
        @SerializedName("totalLeads")
        val totalLeads: Int,
        @SerializedName("Total Validated_Farmers")
        val totalValidatedFarmers: String
    )
}