package com.dhanuka.indhan.MenuScreenActivity.dbmodel;

import androidx.room.Entity;
import androidx.room.PrimaryKey;

@Entity
public class ProductBean {
    public String product;
    public String crop_id;
    @PrimaryKey(autoGenerate = true)
    public int _id;
    public String product_id;
}
