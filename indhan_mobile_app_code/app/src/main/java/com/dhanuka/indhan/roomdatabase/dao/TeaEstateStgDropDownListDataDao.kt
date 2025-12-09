package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateStgHeadOfficeData

@Dao
interface TeaEstateStgDropDownListDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: TeaEstateStgHeadOfficeData?): Long

    @Query("SELECT * FROM TeaEstateStgHeadOfficeData WHERE type =:type")
    fun allTeaEstate(type:String): List<TeaEstateStgHeadOfficeData?>?

    @Query("DELETE FROM TeaEstateStgHeadOfficeData")
    fun deleteTableData()
}