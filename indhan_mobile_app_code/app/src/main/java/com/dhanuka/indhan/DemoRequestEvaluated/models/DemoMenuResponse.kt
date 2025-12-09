package com.dhanuka.indhan.DemoRequestEvaluated.models

import androidx.room.Entity
import androidx.room.PrimaryKey

data class DemoMenuResponse(
    val `data`: List<DemoSubMenu>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class DemoSubMenu(
    @PrimaryKey(autoGenerate = true)
    val id: Int?,
    val base_url: String?,
    val item: String?,
    val tag_icon: String?
)