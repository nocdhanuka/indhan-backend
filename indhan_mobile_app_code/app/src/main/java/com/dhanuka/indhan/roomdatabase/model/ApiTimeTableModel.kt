package com.dhanuka.indhan.roomdatabase.model

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "ApiTimeTable")
class ApiTimeTableModel {

    @PrimaryKey(autoGenerate = true)
    var id: Int? = null

    var updatedAt: String? = null

    var apiName: String? = null

    var typeId: String? = "0"
}