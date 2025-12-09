package com.dhanuka.indhan.DemoRequestEvaluated.models

import androidx.room.Entity
import androidx.room.PrimaryKey

data class RequestedMaterialResponse(
    val crr_date: String,
    val `data`: List<RequestedMaterialResponseData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class RequestedMaterialResponseData(
    @PrimaryKey
    val demo_material_request_id: Int,
    val actual_dispatch_date: String?,
    val admin_approval_comment: String?,
    val admin_approval_date: String?,
    val admin_approval_status: Int?,
    val created_at: String?,
    val crop_id: Int?,
    val crop_name: String?,
    val dispatch_status: Int?,
    val dispatch_status_timestamp: String?,
    val dispatch_units: String?,
    val et_dispatch_date: String?,
    val expected_demos: String?,
    val godown_id: Int?,
    val godown_name: String?,
    val need_by_date: String?,
    val product_id: Int?,
    val product_name: String?,
    val received_comment: String?,
    val received_unit: String?,
    val receiving_date: String?,
    val receiving_status: Int?,
    val receiving_status_captured_date: String?,
    val requested_by_ecn: String?,
    val requested_units: String?,
    val rsm_approval_comment: String?,
    val rsm_approval_date: String?,
    val rsm_approval_status: Int?,
    val rsm_approved_unit: String?,
    val rsm_ecn_name: String?,
    val rsm_ecn_no: String?,
    val s1_ecn_name: String?,
    val s1_ecn_no: String?,
    val s1_ecn_type: String?,
    val s2_ecn_no: String?,
    val s2_ecn_type: String?,
    val updated_at: String?,
    val updated_status_timestamp: String?
)