package com.dhanuka.indhan.DemoRequestActivity.models

import java.io.Serializable

data class DemoRequestListResponse(
    val data: List<DemoRequestData>,
    val message: String,
    val status: String,
    val status_code: Int
) : Serializable {
    data class Data(
        val Excellent: Int,
        val Average: Int,
        val Poor: Int,
        val Overdue: Int,
        val Awaited: Int,
        val demolist: List<DemoRequestData>


    ) : Serializable
}

