package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.bumptech.glide.Glide
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemHighlightBinding


class HighlightAdapter(var context: Context, var list: MutableList<HighlightModel.Result>) :
    RecyclerView.Adapter<HighlightAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemHighlightBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_highlight, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemHighlightBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {
//
            view.txtName.text = list[position].username

//            view.txtProduct.text = list[position].product
            view.txtDetails.text = list[position].description
//
//            view.txtTestimonialId.text=list[position].uniqueTestimonialId
////            view.imgTestimonial.visibility= View.GONE
//
//            //view.imgTestimonial.setImageBitmap(retriveVideoFrameFromVideo(list[position].uploadedFile))
//
//
            if (list[position].highlightImage != "") {
                Glide.with(context)
                    .asBitmap()
                    .load(list[position].highlightImage)
//                    .centerCrop()
//                    .crossFade()
                    .into(view.imgTestimonial)
            } else {
                view.imgTestimonial.visibility = View.GONE
            }
//
//            view.imgTestimonial.setOnClickListener {
//                val intent = Intent(context, WebViewActivity::class.java)
//                intent.putExtra("key",list[position].uploadedFile)
//                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
//                context.startActivity(intent)
//            }//
////            view.llayout.setOnClickListener {
////                val intent = Intent(context, FarmerDetailsActivity::class.java)
////                intent.putExtra("data",list[position])
////                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
////                context.startActivity(intent)
////            }

        }

    }


}