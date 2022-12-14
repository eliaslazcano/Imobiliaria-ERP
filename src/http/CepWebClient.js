import axios from 'axios';

export default class CepWebClient {
  cep;

  constructor(cep) {
    this.cep = cep;
  }

  /**
   * Obtem dados a partir da API POSTMON
   * @returns {Promise<boolean|{bairro: string, cidade: string, logradouro: string, estado: string}>}
   */
  async postmon() {
    const response = await axios.get(`https://api.postmon.com.br/v1/cep/${this.cep}`, {validateStatus: status => status===200 || status===404});
    if (response.status !== 200) return false;
    if (!response.data || !response.data.estado || !response.data.cidade) return false;
    return response.data;
  }

  /**
   * Obtem dados a partir da API VIACEP
   * @returns {Promise<boolean|{bairro: string, localidade: string, logradouro: string, uf: string}>}
   */
  async viacep() {
    const response = await axios.get(`https://viacep.com.br/ws/${this.cep}/json/`);
    if (response.status !== 200) return false;
    if (!response.data || !response.data.uf || !response.data.localidade) return false;
    return response.data;
  }

  /**
   * Tenta obter os dados a partir de qualquer API. Caso uma falhe, será tentado em outra.
   * Se nao encontrar o endereco retorna null, se falhar retorna false.
   * @returns {Promise<false|null|{bairro: string, cidade: string, logradouro: string, estado: string}>}
   */
  async smart() {
    try {
      const postmon = await this.postmon();
      if (postmon) return postmon;
      const viacep = await this.viacep();
      if (viacep) return {estado: viacep.uf, cidade: viacep.localidade, bairro: viacep.bairro, logradouro: viacep.logradouro};
    } catch (e) {
      return false;
    }
    return null;
  }

  /**
   * Tenta obter os dados a partir de qualquer API. Caso uma falhe, será tentado em outra.
   * @returns {Promise<boolean|{bairro: string, cidade: string, logradouro: string, estado: string}>}
   */
  static async smart(cep) {
    const cepWebClient = new CepWebClient(cep);
    return await cepWebClient.smart();
  }


}