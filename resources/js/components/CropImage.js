import React, { useRef } from 'react';
import styled from 'styled-components';
import Cropper from 'react-cropper';

const StyledCropImage = styled.div`
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 100vw;
  button {
    position: absolute;
    padding: 5px;
    background-color: #fff;
    font-size: 14px;
    color: #000;
    border-radius: 3px;
    border: none;

    &:first-of-type {
      top: 10px;
      left: 10px;
    }
    &:last-of-type {
      top: 10px;
      right: 10px;
    }
  }
`;

const CropImage = ({ image, handleCroppedImage, close, fromCreatePost }) => {
  const cropper = useRef(null);
  return (
    <StyledCropImage>
      <Cropper
        ref={cropper}
        src={image}
        style={{
          width: '100%',
          height: '100%'
        }}
        dragMode="move"
        aspectRatio={1 / 1}
      />
      <button onClick={close}>Cancel</button>
      <button
        onClick={() => {
          handleCroppedImage(cropper.current.getCroppedCanvas().toDataURL());
          !fromCreatePost && close();
        }}
      >
        Done
      </button>
    </StyledCropImage>
  );
};

export default CropImage;
