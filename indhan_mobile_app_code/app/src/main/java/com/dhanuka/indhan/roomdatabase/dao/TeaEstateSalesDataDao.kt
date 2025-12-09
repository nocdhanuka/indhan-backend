package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.TeaEstateSalesData

@Dao
interface TeaEstateSalesDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: TeaEstateSalesData)

    @Query("SELECT * FROM TeaEstateSalesData")
    fun getAllSalesListData(): List<TeaEstateSalesData?>?

    @Query("SELECT * FROM TeaEstateSalesData WHERE tea_estate_id =:id")
    fun getAllSalesById(id: Int): List<TeaEstateSalesData?>?

    @Query("SELECT * FROM TeaEstateSalesData WHERE tea_estate_id =:id AND month=:month AND year=:year")
    fun getAllSalesById(id: Int, month: String, year: String): List<TeaEstateSalesData?>?

    @Query("SELECT * FROM TeaEstateSalesData WHERE month=:month AND year=:year")
    fun getAllSalesById(month: String, year: String): List<TeaEstateSalesData?>?

    @Query("DELETE FROM TeaEstateSalesData WHERE tea_estate_id =:id")
    fun deleteTableData(id: Int)
}