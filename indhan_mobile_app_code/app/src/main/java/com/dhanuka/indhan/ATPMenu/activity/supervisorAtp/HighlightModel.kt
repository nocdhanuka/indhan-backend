package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp


import com.google.gson.annotations.SerializedName

data class HighlightModel(
    @SerializedName("message")
    val message: String, // Highlight submitted successfully.
    @SerializedName("result")
    val result: MutableList<Result>,
    @SerializedName("status")
    val status: String, // success
    @SerializedName("status_code")
    val statusCode: Int // 200
) {
    data class Result(
        @SerializedName("atpfeedback_id")
        val atpfeedbackId: String, // 1
        @SerializedName("creation_at")
        val creationAt: String, // 2019-09-11 12:54:32
        @SerializedName("description")
        val description: String, // dfghj,eghj
        @SerializedName("highlightImage")
        val highlightImage: String, // http://14.142.56.172:8088/DhanukaV2dev/assets/highlightImage/1568186672962.png
        @SerializedName("ticket_id")
        val ticketId: String, // 48179
        @SerializedName("updation_at")
        val updationAt: String, // 0000-00-00 00:00:00
        @SerializedName("user_id")
        val userId: String, // 1
        @SerializedName("user_type")
        val userType: String, // marketing_supervisor

        @SerializedName("username")
        val username: String // marketing_supervisor
    )
}