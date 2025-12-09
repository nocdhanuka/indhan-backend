package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateStgData

@Dao
interface TeaEstateStgListDataDao {
    @Insert
    fun insert(data: TeaEstateStgData?): Long

    @get:Query("SELECT * FROM TeaEstateStgData")
    val allTeaEstate: List<TeaEstateStgData?>?

    @Query("DELETE FROM TeaEstateStgData")
    fun deleteTableData()
}